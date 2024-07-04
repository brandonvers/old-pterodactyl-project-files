import * as React from 'react';
import ContentBox from '@/components/elements/ContentBox';
import PageContentBlock from '@/components/elements/PageContentBlock';
import tw from 'twin.macro';
import styled from 'styled-components/macro';
import Button, { LinkButton } from "@/components/elements/Button";
import { ApplicationStore } from '@/state';
import { State, useStoreState } from 'easy-peasy';
import { breakpoint } from '@/theme';

const Container = styled.div`
    ${tw`flex flex-wrap`};
    & > div {
        ${tw`w-full`};
        ${breakpoint('md')`
            width: calc(50% - 1rem);
        `}
        ${breakpoint('xl')`
            ${tw`w-auto flex-1`};
        `}
    }
`;

export default () => {	
	
    const { drivers } = useStoreState<ApplicationStore, any>(state => state.settings.data!.oauth);
    const oauth = JSON.parse(useStoreState((state: State<ApplicationStore>) => state.user.data!.oauth));

    return (
        <PageContentBlock title={'OAuth Setup'}>
            <Container css={[ tw`mb-10 mt-10` ]}>
                <div>
						{JSON.parse(drivers).map((driver: string) => (
							<ContentBox
								key={driver}
								css={tw`mt-8 md:mt-0 mx-4 my-4 inline-block`}
							>
								<div css={tw`w-48`}>
									<img src={'/assets/svgs/' + driver + '.svg'} css={tw`w-16 float-right`} alt={driver}/>
									<h3>{driver.charAt(0).toUpperCase() + driver.slice(1)}</h3>

									{ oauth[driver] != null &&
									<div>
										<div css={tw`mt-8 mb-4`}>
											Linked with id <code>{oauth[driver]}</code>
										</div>
										<div css={tw`mb-4`}>
											<LinkButton
												color={'red'}
												isSecondary
												href={`/account/oauth/unlink?driver=${driver}`}
											>
												Unlink {driver}
											</LinkButton>
										</div>
									</div>
									}
									{ oauth[driver] == null &&
									<div>
										<div css={tw`mt-8 mb-4`}>
											Not linked with {driver.charAt(0).toUpperCase() + driver.slice(1)}
										</div>
										<div css={tw`mb-4`}>
											<LinkButton
												color={'green'}
												isSecondary
												href={`/account/oauth/link?driver=${driver}`}
											>
												Link {driver}
											</LinkButton>
										</div>
									</div>
									}

								</div>
							</ContentBox>
						))}
                </div>
            </Container>
        </PageContentBlock>
    );
};