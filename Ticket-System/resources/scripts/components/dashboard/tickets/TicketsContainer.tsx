import React, { useEffect, useState } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import useFlash from '@/plugins/useFlash';
import tw from 'twin.macro';
import useSWR from 'swr';
import Spinner from '@/components/elements/Spinner';
import GreyRowBox from '@/components/elements/GreyRowBox';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faTicketAlt } from '@fortawesome/free-solid-svg-icons';
import styled from 'styled-components/macro';
import { Link } from 'react-router-dom';
import FlashMessageRender from '@/components/FlashMessageRender';
import MessageBox from '@/components/MessageBox';

import getTickets from '@/api/tickets/getTickets';

const Code = styled.code`${tw`font-mono py-1 px-2 bg-neutral-900 rounded text-sm inline-block`}`;

export interface TicketsResponse {
    tickets: any[];
    categories: any[];
    priorities: any[];
}


export default () => {
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<TicketsResponse>([ '/tickets' ], () => getTickets());

    useEffect(() => {
        if (!error) {
            clearFlashes('tickets');
        } else {
            clearAndAddHttpError({ key: 'tickets', error });
        }
    });

    return (
        <PageContentBlock title={'Tickets'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'tickets'} css={tw`mb-4`} />
            </div>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'tickets:create'} css={tw`mb-4`} />
            </div>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <>
                    <div css={tw`w-full`}>
                        {data.tickets.length < 1 ?
                            <MessageBox type="info" title="Info">
                                There are no tickets.
                            </MessageBox>
                            :
                            (data.tickets.map((item, key) => (
                                <GreyRowBox as={Link} to={`/tickets/${item.id}`} css={tw`mb-2`} key={key}>
                                    <div css={tw`hidden md:block`}>
                                        <FontAwesomeIcon icon={faTicketAlt} fixedWidth/>
                                    </div>
                                    <div css={tw`flex-1 ml-4`}>
                                        {item.status === 1 ?
                                        <span css={tw`bg-green-500 py-1 px-2 rounded text-white text-xs md:w-full`}>Open</span>
                                        : item.status === 2 ?
                                        <span css={tw`bg-cyan-500 py-1 px-2 rounded text-white text-xs md:w-full`}>Answered</span>
                                        : item.status === 3 ?
                                        <span css={tw`bg-primary-500 py-1 px-2 rounded text-white text-xs md:w-full`}>Customer-Reply</span>
                                        : item.status === 4 ?
                                        <span css={tw`bg-yellow-500 py-1 px-2 rounded text-white text-xs md:w-full`}>On Hold</span>
                                        : item.status === 5 ?
                                        <span css={tw`bg-neutral-500 py-1 px-2 rounded text-white text-xs md:w-full`}>In Progress</span>
                                        :
                                        <span css={tw`bg-red-500 py-1 px-2 rounded text-white text-xs md:w-full`}>Closed</span>
                                        }
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none md:w-full`}>Status</p>
                                    </div>
                                    <div css={tw`ml-8 text-center`}>
                                        <p css={tw`text-sm`}><Code>#{item.ticket_id}</Code></p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>ID</p>
                                    </div>
                                    <div css={tw`flex-1 ml-4`}>
                                        <p css={tw`text-sm`}>{item.title.substr(0, 40) + (item.title.length > 40 ? '...' : '')}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Title</p>
                                    </div>
                                    <div css={tw`ml-8 text-center hidden md:block`}>
                                        <p css={tw`text-sm`}>{item.category.name}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Category</p>
                                    </div>
                                    <div css={tw`ml-8 text-center hidden md:block`}>
                                        <p css={tw`text-sm`}>{item.priority.name}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Priority</p>
                                    </div>
                                    <div css={tw`ml-8 text-center hidden md:block`}>
                                        <p css={tw`text-sm`}>{item.updated_at}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Updated</p>
                                    </div>
                                </GreyRowBox>
                            )))
                        }
                    </div>
                </>
            }
        </PageContentBlock>
    );
};
